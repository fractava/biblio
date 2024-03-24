<template>
	<div>
		<div class="section">
			<SectionHeader>
				{{ t("biblio", "Item Properties") }}
			</SectionHeader>
			<EditModeButton :edit-mode.sync="editMode" />
			<FieldsValueTable :field-values="item.fieldValues"
				:edit-mode="editMode"
				@update:value="updateValue">
				<template #head>
					<tr>
						<td>{{ t("biblio", "Title") }}</td>
						<td>
							<ShortTextFieldValue :field-type="FieldTypes['short']"
								:value="item.title"
								:allow-value-edit="editMode"
								@update:value="updateTitle" />
						</td>
					</tr>
				</template>
			</FieldsValueTable>
		</div>
		<div class="section">
			<SectionHeader>
				{{ t("biblio", "Item Instances") }}
			</SectionHeader>
			<AddItemInstanceModal :open.sync="modalOpen"
				:item-id="itemId"
				:default-prefix="barcodePrefix"
				@added-instance="onAddedInstance" />
			<DataTable :columns="columns"
				:rows="searchResults"
				:page="page"
				:max-page="maxPage"
				:create-row-label="t('biblio', 'Create Instance')"
				:create-row-description="t('biblio', 'This Item currently has no Instance, that fits the search parameters')"
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
import AddItemInstanceModal from "../components/AddItemInstanceModal.vue";
import EditModeButton from "../components/EditModeButton.vue";
import SectionHeader from "../components/SectionHeader.vue";

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
		AddItemInstanceModal,
		DataTable,
		SectionHeader,
	},
	props: {
	},
	data() {
		return {
			FieldTypes,
			editMode: false,
			item: {},
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
		itemId() {
			return parseInt(this.$route.params.id);
		},
		maxPage() {
			return getMaxPage(this.searchMeta.totalResultCount, this.limit);
		},
		columns() {
			return getItemInstanceColumns(true, false, true, this.itemInstancesStore.sortedFields);
		},
	},
	watch: {
		maxPage() {
			this.ensurePageIsNotExceedingMax();
		},
	},
	mounted() {
		api.getItem(this.$route.params.collectionId, this.itemId).then((result) => {
			this.item = result;
		});
		this.refreshBarcodePrefix();
		this.refreshSearchResults();
	},
	methods: {
		updateTitle(title) {
			// optimistic update
			this.item.title = title;

			api.updateItem(this.$route.params.collectionId, this.itemId, {
				title,
			});
		},
		updateValue(newValue, field) {
			api.updateItemFieldValue(this.$route.params.collectionId, this.itemId, field.fieldId, {
				value: newValue,
			}).then((result) => {
				const updatedIndex = this.item.fieldValues.findIndex(fieldValue => fieldValue.fieldId === field.fieldId);
				console.log(result, updatedIndex);
				Vue.set(this.item.fieldValues, updatedIndex, result);
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

				filters.item_id = {
					operator: "=",
					operand: this.itemId,
				};

				const offset = (this.page - 1) * this.limit;

				const apiPromise = api.getItemInstances(route.params.collectionId, "model+loan+loan_customer+fields", filters, this.sort, this.sortReverse, this.limit, offset);

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
		refreshBarcodePrefix() {
			api.getItemInstancesBarcodePrefix(this.$route.params.collectionId, this.itemId).then((result) => {
				this.barcodePrefix = result;
			});
		},
		onAddedInstance() {
			this.refreshSearchResults();
			this.refreshBarcodePrefix();
		},
		openItemInstance(itemInstanceId, columnId) {
			const itemInstance = this.getItemInstanceById(itemInstanceId);
			if (columnId === -3) {
				if (itemInstance.loan.customerId) {
					this.$router.push({
						name: "customer",
						params: {
							...this.$route.params,
							id: itemInstance.loan.customerId,
						},
					});
				}
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
}
</style>
