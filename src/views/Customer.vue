<template>
	<div>
		<div class="section">
			<h1 class="sectionHeader">
				{{ t("biblio", "Customer Properties") }}
			</h1>
			<EditModeButton :edit-mode.sync="editMode" />
			<FieldsValueTable :field-values="customer.fieldValues"
				:edit-mode="editMode"
				@update:value="updateValue">
				<template #head>
					<tr>
						<td>{{ t("biblio", "Name") }}</td>
						<td>
							<ShortTextFieldValue :field-type="FieldTypes['short']"
								:value="customer.name"
								:allow-value-edit="editMode"
								@update:value="updateName" />
						</td>
					</tr>
				</template>
			</FieldsValueTable>
		</div>
		<div class="section">
			<h1 class="sectionHeader">
				{{ t("biblio", "Customer Loans") }}
			</h1>
			<DataTable :columns="columns"
				:rows="searchResults"
				:page="page"
				:max-page="maxPage"
				:can-create-rows="false"
				:current-sort-reverse="sortReverse"
				:current-filters="filters"
				:current-sort="sort"
				:row-limit-filter="limit"
				@create-row="modalOpen = true"
				@set-search-string="onSearchUpdate"
				@update:currentFilters="onFieltersUpdate"
				@update:currentSort="onSortUpdate"
				@update:currentSortReverse="onSortReverseUpdate"
				@update:rowLimitFilter="onRowLimitFilterUpdate"
				@update:page="onPageUpdate"
				@click-row="openItemInstance"
				@delete-selected-rows="deleteItemInstances" />
		</div>
	</div>
</template>

<script>
import Vue from "vue";
import { mapStores } from "pinia";
import PCancelable from "p-cancelable";
import debounceFn from "debounce-fn";
import { showError } from "@nextcloud/dialogs";

import { api } from "../api.js";

import ShortTextFieldValue from "../components/Fields/Values/ShortTextFieldValue.vue";
import FieldsValueTable from "../components/FieldsValueTable.vue";
import DataTable from "../components/dataTable/DataTable.vue";
import EditModeButton from "../components/EditModeButton.vue";

import { useBiblioStore } from "../store/biblio.js";
import { useItemInstancesStore } from "../store/itemInstances.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

import { getMaxPage, getItemInstanceColumns } from "../utils/dataTableHelpers.js";

import FieldTypes from "../models/FieldTypes.js";

export default {
	components: {
		EditModeButton,
		ShortTextFieldValue,
		FieldsValueTable,
		DataTable,
	},
	props: {
	},
	data() {
		return {
			FieldTypes,
			editMode: false,
			customer: {},
			modalOpen: false,
			searchResults: [],
			searchMeta: {},
			search: "",
			filters: {},
			sort: "barcode",
			sortReverse: false,
			limit: 100,
			page: 1,
			currentlyRunningFetch: false,
			barcodePrefix: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore, useItemInstancesStore, useNomenclatureStore),
		customerId() {
			return parseInt(this.$route.params.id);
		},
		maxPage() {
			return getMaxPage(this.searchMeta.totalResultCount, this.limit);
		},
		columns() {
			return getItemInstanceColumns(true, true, false, this.itemInstancesStore.sortedFields);
		},
	},
	watch: {
		maxPage() {
			this.ensurePageIsNotExceedingMax();
		},
	},
	mounted() {
		api.getCustomer(this.$route.params.collectionId, this.customerId).then((result) => {
			this.customer = result;
		});
		this.refreshSearchResults();
	},
	methods: {
		updateName(name) {
			// optimistic update
			this.customer.name = name;

			api.updateCustomer(this.$route.params.collectionId, this.customerId, {
				name,
			});
		},
		updateValue(newValue, field) {
			api.updateCustomerFieldValue(this.$route.params.collectionId, this.customerId, field.fieldId, {
				value: newValue,
			}).then((result) => {
				const updatedIndex = this.customer.fieldValues.findIndex(fieldValue => fieldValue.fieldId === field.fieldId);
				console.log(result, updatedIndex);
				Vue.set(this.customer.fieldValues, updatedIndex, result);
			});
		},
		refreshSearchResults() {
			const route = window.biblioRouter.currentRoute;

			if (this.currentlyRunningFetch) {
				this.currentlyRunningFetch.cancel();
			}

			const newItemInstancesFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!route.params.collectionId) {
					this.searchResults = [];
					return resolve();
				}

				const filters = Object.assign({}, this.filters);

				if (this.search) {
					filters.barcode = {
						operator: "contains",
						operand: this.search,
					};
				}

				filters.loan_customer_id = {
					operator: "=",
					operand: this.customerId,
				};

				const offset = (this.page - 1) * this.limit;

				const apiPromise = api.getItemInstances(route.params.collectionId, "model+loan+item+fields", filters, this.sort, this.sortReverse, this.limit, offset);

				apiPromise.then((result) => {
					this.searchResults = result.instances;
					this.searchMeta = result.meta;
					resolve();
				})
					.catch((error) => {
						if (!apiPromise.isCanceled) {
							console.error(error);
							showError(t("biblio", "Could not fetch item instances"));
						}

						resolve();
					});

				onCancel(() => {
					apiPromise.cancel();
				});
			});

			this.currentlyRunningFetch = newItemInstancesFetch;

			return newItemInstancesFetch;
		},
		onAddedInstance() {
			this.refreshSearchResults();
		},
		openItemInstance(itemInstanceId, columnId) {
			const itemInstance = this.getItemInstanceById(itemInstanceId);
			if (columnId === -2) {
				this.$router.push({
					name: "item",
					params: {
						...this.$route.params,
						id: itemInstance.itemId,
					},
				});
			}
		},
		onSearchUpdate(newSearch) {
			this.search = newSearch;
			this.refreshSearch();
		},
		onFieltersUpdate(newFilters) {
			this.filters = newFilters;
			this.refreshSearch();
		},
		onSortUpdate(newSort) {
			this.sort = newSort;
			this.refreshSearch();
		},
		onSortReverseUpdate(newSortReverse) {
			this.sortReverse = newSortReverse;
			this.refreshSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.limit = newLimit;
			this.ensurePageIsNotExceedingMax();
			this.refreshSearch();
		},
		onPageUpdate(newPage) {
			this.page = newPage;
			this.refreshSearch();
		},
		refreshSearch: debounceFn(function() {
			const refreshPromise = this.refreshSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
		ensurePageIsNotExceedingMax() {
			if (this.page > this.maxPage) {
				this.page = this.maxPage;
			}
		},
		deleteItemInstances(itemInstanceIds) {
			for (const itemInstanceId of itemInstanceIds) {
				api.deleteItemInstance(this.$route.params.collectionId, itemInstanceId);
			}

			this.refreshSearch();
		},
		getItemInstanceById(id) {
			return this.searchResults.find(instance => instance.id === id);
		},
	},
};
</script>

<style lang="scss" scoped>

.section {
	margin-bottom: 1.34em;

	.sectionHeader {
		text-align:center;
		font-size: 2em;
		font-weight: bold;
	}
}
</style>
