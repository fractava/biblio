<template>
	<NoCollectionSelected>
		<ul>
			<AddItemModal :open.sync="modalOpen" />
			<DataTable class="itemInstancesDataTable"
				:columns="columns"
				:rows="itemInstancesStore.searchResults"
				:page="itemInstancesStore.page"
				:max-page="maxPage"
				:can-create-rows="false"
				:current-sort="itemInstancesStore.sort"
				:current-sort-reverse="itemInstancesStore.sortReverse"
				:current-filters="itemInstancesStore.filters"
				:row-limit-filter="itemInstancesStore.limit"
				@create-row="modalOpen = true"
				@set-search-string="onSearchUpdate"
				@update:currentFilters="onFieltersUpdate"
				@update:currentSort="onSortUpdate"
				@update:currentSortReverse="onSortReverseUpdate"
				@update:rowLimitFilter="onRowLimitFilterUpdate"
				@update:page="onPageUpdate"
				@click-row="openItemInstance"
				@delete-selected-rows="deleteItemInstances" />
		</ul>
	</NoCollectionSelected>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import DataTable from "../components/dataTable/DataTable.vue";
import { useItemInstancesStore } from "../store/itemInstances.js";

import AddItemModal from "../components/AddItemModal.vue";
import NoCollectionSelected from "../components/NoCollectionSelected.vue";

import FieldTypes from "../models/FieldTypes.js";
import TextCell from "../components/Fields/Cells/TextCell.vue";

export default {
	components: {
		DataTable,
		AddItemModal,
		NoCollectionSelected,
	},
	data() {
		return {
			modalOpen: false,
		};
	},
	computed: {
		...mapStores(useItemInstancesStore),
		maxPage() {
			return Math.ceil(Math.max(this.itemInstancesStore.searchMeta.totalResultCount, 1) / this.itemInstancesStore.limit) || 1;
		},
		columns() {
			const fieldColumns = this.itemInstancesStore.includedSortedFields.map((field) => {
				const type = FieldTypes[field.type];
				return {
					id: field.id,
					name: field.name,
					type: field.type,
					isProperty: false,
					canSort: type.canSort,
					sortIdentifier: `field:${field.id}`,
					canFilter: true,
					filterOperators: type.filterOperators,
					filterOperandType: type.filterOperandType,
					filterOperandOptions: field?.settings?.options,
					cellComponent: type.valueCellComponent,
					defaultValue: type.defaultValue,
					defaultSettings: type.defaultSettings,
				};
			});

			return [
				{
					id: -1,
					name: "Barcode",
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "barcode",
					canFilter: false,
					property: "barcode",
					cellComponent: TextCell,
				},
				{
					id: -2,
					name: "Item Title",
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "item_title",
					canFilter: false,
					property: ["item", "title"],
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
		this.itemInstancesStore.refreshSearchResults();
	},
	methods: {
		openItemInstance(itemId) {
		},
		onSearchUpdate(newSearch) {
			this.itemInstancesStore.search = newSearch;
			this.refreshSearch();
		},
		onFieltersUpdate(newFilters) {
			this.itemInstancesStore.filters = newFilters;
			this.refreshSearch();
		},
		onSortUpdate(newSort) {
			this.itemInstancesStore.sort = newSort;
			this.refreshSearch();
		},
		onSortReverseUpdate(newSortReverse) {
			this.itemInstancesStore.sortReverse = newSortReverse;
			this.refreshSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.itemInstancesStore.limit = newLimit;
			this.ensurePageIsNotExceedingMax();
			this.refreshSearch();
		},
		onPageUpdate(newPage) {
			this.itemInstancesStore.page = newPage;
			this.refreshSearch();
		},
		refreshSearch: debounceFn(() => {
			const itemInstancesStore = useItemInstancesStore();

			const refreshPromise = itemInstancesStore.refreshSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
		ensurePageIsNotExceedingMax() {
			if (this.itemInstancesStore.page > this.maxPage) {
				this.itemInstancesStore.page = this.maxPage;
			}
		},
		deleteItemInstances(itemInstanceIds) {
			for (const itemInstanceId of itemInstanceIds) {
				this.itemInstancesStore.deleteItemInstance(itemInstanceId);
			}
		},
	},
};

</script>
<style scoped>
.itemInstancesDataTable {
	margin-bottom: 330px;
}
</style>
