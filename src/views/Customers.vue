<template>
	<div>
		<ul>
			<AddCustomerModal :open.sync="modalOpen" />
			<DataTable class="customersDataTable"
				:columns="columns"
				:rows="customersStore.searchResults"
				:page="customersStore.page"
				:max-page="maxPage"
				:can-create-rows="true"
				:current-sort="customersStore.sort"
				:current-sort-reverse="customersStore.sortReverse"
				:current-filters="customersStore.filters"
				:row-limit-filter="customersStore.limit"
				:create-row-label="nomenclatureStore.createCustomer"
				:create-row-description="nomenclatureStore.noCustomers"
				@create-row="modalOpen = true"
				@set-search-string="onSearchUpdate"
				@update:currentFilters="onFieltersUpdate"
				@update:currentSort="onSortUpdate"
				@update:currentSortReverse="onSortReverseUpdate"
				@update:rowLimitFilter="onRowLimitFilterUpdate"
				@update:page="onPageUpdate"
				@click-row="openCustomer"
				@delete-selected-rows="deleteCustomers">
				<template #footer>
					<PaginatedDataTableFooter>
						<span>
							{{ t("biblio", "Customers {firstItemIndex} to {lastItemIndex} visible of {totalRows} customers in total", {
								firstItemIndex,
								lastItemIndex,
								totalRows: customersStore.searchMeta.totalResultCount,
							}) }}
						</span>
					</PaginatedDataTableFooter>
				</template>
			</DataTable>
		</ul>
	</div>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import DataTable from "../components/dataTable/DataTable.vue";
import { useCustomersStore } from "../store/customers.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

import AddCustomerModal from "../components/AddCustomerModal.vue";
import TextCell from "../components/Fields/Cells/TextCell.vue";
import PaginatedDataTableFooter from "../components/PaginatedDataTableFooter.vue";

import FieldTypes from "../models/FieldTypes.js";
import { getMaxPage, firstItemIndex, lastItemIndex } from "../utils/dataTableHelpers.js";

export default {
	components: {
		DataTable,
		AddCustomerModal,
		PaginatedDataTableFooter,
	},
	data() {
		return {
			modalOpen: false,
		};
	},
	computed: {
		...mapStores(useCustomersStore, useNomenclatureStore),
		maxPage() {
			return getMaxPage(this.customersStore.searchMeta.totalResultCount, this.customersStore.limit);
		},
		columns() {
			const fieldColumns = this.customersStore.includedSortedFields.map((field) => {
				const type = FieldTypes[field.type];
				return {
					id: field.id,
					name: field.name,
					type: field.type,
					isProperty: false,
					canSort: type.canSort,
					sortIdentifier: `field:${field.id}`,
					canFilter: true,
					clickable: true,
					filterOperandType: type.filterOperandType,
					filterOperandOptions: type.filterOperandOptions || field?.settings?.options,
					filterOperators: type.filterOperators,
					cellComponent: type.valueCellComponent,
					defaultValue: type.defaultValue,
					defaultSettings: type.defaultSettings,
				};
			});

			return [
				{
					id: -1,
					name: t("biblio", "Name"),
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "name",
					canFilter: false,
					clickable: true,
					property: "name",
					filterOperators: FieldTypes?.short?.filterOperators,
					cellComponent: TextCell,
				},
				...fieldColumns,
			];
		},
		firstItemIndex() {
			return firstItemIndex(this.customersStore.page, this.customersStore.limit);
		},
		lastItemIndex() {
			return lastItemIndex(this.firstItemIndex, this.customersStore.searchResults.length);
		},
	},
	watch: {
		maxPage() {
			this.ensurePageIsNotExceedingMax();
		},
	},
	mounted() {
		this.customersStore.refreshSearchResults();
	},
	methods: {
		openCustomer(customerId) {
			this.$router.push({
				name: "customer",
				params: {
					...this.$route.params,
					id: customerId,
				},
			});
		},
		onSearchUpdate(newSearch) {
			this.customersStore.search = newSearch;
			this.refreshSearch();
		},
		onFieltersUpdate(newFilters) {
			this.customersStore.filters = newFilters;
			this.refreshSearch();
		},
		onSortUpdate(newSort) {
			this.customersStore.sort = newSort;
			this.refreshSearch();
		},
		onSortReverseUpdate(newSortReverse) {
			this.customersStore.sortReverse = newSortReverse;
			this.refreshSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.customersStore.limit = newLimit;
			this.ensurePageIsNotExceedingMax();
			this.refreshSearch();
		},
		onPageUpdate(newPage) {
			this.customersStore.page = newPage;
			this.refreshSearch();
		},
		refreshSearch: debounceFn(function() {
			const customersStore = useCustomersStore();

			const refreshPromise = customersStore.refreshSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
		ensurePageIsNotExceedingMax() {
			if (this.customersStore.page > this.maxPage) {
				this.customersStore.page = this.maxPage;
			}
		},
		deleteCustomers(customerIds) {
			for (const customerId of customerIds) {
				this.customersStore.deleteCustomer(customerId);
			}
		},
	},
};

</script>
<style scoped>
.customersDataTable {
	margin-bottom: 330px;
}
</style>
