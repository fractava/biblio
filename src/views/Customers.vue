<template>
	<NoCollectionSelected>
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
				:create-row-label="t('biblio', 'Create Customer')"
				:create-row-description="t('biblio', 'There are currently no customers in this collection, that fit the search parameters')"
				@create-row="modalOpen = true"
				@set-search-string="onSearchUpdate"
				@update:currentFilters="onFieltersUpdate"
				@update:currentSort="onSortUpdate"
				@update:currentSortReverse="onSortReverseUpdate"
				@update:rowLimitFilter="onRowLimitFilterUpdate"
				@update:page="onPageUpdate"
				@click-row="openCustomer"
				@delete-selected-rows="deleteCustomers" />
		</ul>
	</NoCollectionSelected>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import DataTable from "../components/dataTable/DataTable.vue";
import { useCustomersStore } from "../store/customers.js";

import AddCustomerModal from "../components/AddCustomerModal.vue";
import NoCollectionSelected from "../components/NoCollectionSelected.vue";

import FieldTypes from "../models/FieldTypes";
import TextCell from '../components/Fields/Cells/TextCell.vue';

export default {
	components: {
		DataTable,
		AddCustomerModal,
		NoCollectionSelected,
	},
	data() {
		return {
			modalOpen: false,
		};
	},
	computed: {
		...mapStores(useCustomersStore),
		maxPage() {
			return Math.ceil(Math.max(this.customersStore.searchMeta.totalResultCount, 1) / this.customersStore.limit) || 1;
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
					filterOperandOptions: field?.settings?.options,
					filterOperators: type.filterOperators,
					cellComponent: type.valueCellComponent,
					defaultValue: type.defaultValue,
					defaultSettings: type.defaultSettings,
				};
			});

			return [
				{
					id: -1,
					name: "Name",
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
				path: "/customer/" + customerId,
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
		refreshSearch: debounceFn(() => {
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
