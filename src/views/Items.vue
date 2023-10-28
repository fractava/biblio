<template>
	<div>
		<ul>
			<AddItemModal :open.sync="modalOpen" />
			<DataTable class="itemsDataTable"
				:columns="columns"
				:rows="itemsStore.searchResults"
				:page="itemsStore.page"
				:max-page="maxPage"
				:can-create-rows="true"
				:current-sort="itemsStore.sort"
				:current-sort-reverse="itemsStore.sortReverse"
				:current-filters="itemsStore.filters"
				:row-limit-filter="itemsStore.limit"
				:create-row-label="nomenclatureStore.createItem"
				:create-row-description="t('biblio', 'There are currently no items in this collection, that fit the search parameters')"
				@create-row="modalOpen = true"
				@set-search-string="onSearchUpdate"
				@update:currentFilters="onFieltersUpdate"
				@update:currentSort="onSortUpdate"
				@update:currentSortReverse="onSortReverseUpdate"
				@update:rowLimitFilter="onRowLimitFilterUpdate"
				@update:page="onPageUpdate"
				@click-row="openItem"
				@delete-selected-rows="deleteItems" />
		</ul>
	</div>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import DataTable from "../components/dataTable/DataTable.vue";
import { useItemsStore } from "../store/items.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

import AddItemModal from "../components/AddItemModal.vue";

import FieldTypes from "../models/FieldTypes";
import TextCell from '../components/Fields/Cells/TextCell.vue';

export default {
	components: {
		DataTable,
		AddItemModal,
	},
	data() {
		return {
			modalOpen: false,
		};
	},
	computed: {
		...mapStores(useItemsStore, useNomenclatureStore),
		maxPage() {
			return Math.ceil(Math.max(this.itemsStore.searchMeta.totalResultCount, 1) / this.itemsStore.limit) || 1;
		},
		columns() {
			const fieldColumns = this.itemsStore.includedSortedFields.map((field) => {
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
					filterOperators: type.filterOperators,
					filterOperandType: type.filterOperandType,
					filterOperandOptions: type.filterOperandOptions || field?.settings?.options,
					cellComponent: type.valueCellComponent,
					defaultValue: type.defaultValue,
					defaultSettings: type.defaultSettings,
				};
			});

			return [
				{
					id: -1,
					name: t("biblio", "Title"),
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "title",
					canFilter: false,
					clickable: true,
					property: "title",
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
		this.itemsStore.refreshSearchResults();
	},
	methods: {
		openItem(itemId) {
			this.$router.push({
				name: "item",
				params: {
					...this.$route.params,
					id: itemId,
				},
			});
		},
		onSearchUpdate(newSearch) {
			this.itemsStore.search = newSearch;
			this.refreshSearch();
		},
		onFieltersUpdate(newFilters) {
			this.itemsStore.filters = newFilters;
			this.refreshSearch();
		},
		onSortUpdate(newSort) {
			this.itemsStore.sort = newSort;
			this.refreshSearch();
		},
		onSortReverseUpdate(newSortReverse) {
			this.itemsStore.sortReverse = newSortReverse;
			this.refreshSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.itemsStore.limit = newLimit;
			this.ensurePageIsNotExceedingMax();
			this.refreshSearch();
		},
		onPageUpdate(newPage) {
			this.itemsStore.page = newPage;
			this.refreshSearch();
		},
		refreshSearch: debounceFn(function() {
			const itemsStore = useItemsStore();

			const refreshPromise = itemsStore.refreshSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
		ensurePageIsNotExceedingMax() {
			if (this.itemsStore.page > this.maxPage) {
				this.itemsStore.page = this.maxPage;
			}
		},
		deleteItems(itemIds) {
			for (const itemId of itemIds) {
				this.itemsStore.deleteItem(itemId);
			}
		},
	},
};

</script>
<style scoped>
.itemsDataTable {
	margin-bottom: 330px;
}
</style>
