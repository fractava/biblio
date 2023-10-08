<template>
	<NoCollectionSelected>
		<ul>
			<AddItemModal :open.sync="modalOpen" />
			<DataTable class="itemsDataTable"
				:columns="columns"
				:rows="biblioStore.itemSearchResults"
				:page="biblioStore.itemPage"
				:max-page="maxPage"
				:can-create-rows="true"
				:current-sort="biblioStore.itemSort"
				:current-sort-reverse="biblioStore.itemSortReverse"
				:current-filters="biblioStore.itemFilters"
				:row-limit-filter="biblioStore.itemLimit"
				:create-row-label="t('biblio', 'Create Item')"
				:create-row-description="t('biblio', 'There are currently no items in this collection, that fit the search parameters')"
				@create-row="modalOpen = true"
				@set-search-string="onItemSearchUpdate"
				@update:currentFilters="onItemFieltersUpdate"
				@update:currentSort="onItemSortUpdate"
				@update:currentSortReverse="onItemSortReverseUpdate"
				@update:rowLimitFilter="onRowLimitFilterUpdate"
				@update:page="onPageUpdate"
				@click-row="openItem" />
		</ul>
	</NoCollectionSelected>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from 'debounce-fn';
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import Table from "../components/Table.vue";
import DataTable from "../components/dataTable/DataTable.vue";
import { useBiblioStore } from "../store/biblio.js";
import AddItemModal from "../components/AddItemModal.vue";
import NoCollectionSelected from "../components/NoCollectionSelected.vue";

import FieldTypes from "../models/FieldTypes";
import TextCell from '../components/Fields/Cells/TextCell.vue';

export default {
	components: {
		NcTextField,
		Table,
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
		...mapStores(useBiblioStore),
		includedItemFields() {
			return this.biblioStore.itemFields.filter((itemField) => (itemField.includeInList));
		},
		maxPage() {
			return Math.ceil(Math.max(this.biblioStore.itemSearchMeta.totalResultCount, 1) / this.biblioStore.itemLimit);
		},
		columns() {
			let itemFieldColumns = this.includedItemFields.map((field) => {
				let type = FieldTypes[field.type];
				return {
					id: field.id,
					name: field.name,
					type: field.type,
					isProperty: false,
					canSort: type.canSort,
					sortIdentifier: `field:${field.id}`,
					canFilter: true,
					filterOperators: type.filterOperators,
					cellComponent: type.valueCellComponent,
					defaultValue: type.defaultValue,
				};
			});

			return [
				{
					id: -1,
					name: "Title",
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "title",
					canFilter: false,
					property: "title",
					filterOperators: FieldTypes?.short?.filterOperators,
					cellComponent: TextCell,
				},
				...itemFieldColumns,
			];
		},
	},
	mounted() {
		this.biblioStore.refreshItemSearchResults();
	},
	methods: {
		openItem(itemId) {
			this.$router.push({
				path: "/item/" + itemId,
			});
		},
		onItemSearchUpdate(newSearch) {
			this.biblioStore.itemSearch = newSearch;
			this.refreshItemSearch();
		},
		onItemFieltersUpdate(newFilters) {
			this.biblioStore.itemFilters = newFilters;
			this.refreshItemSearch();
		},
		onItemSortUpdate(newSort) {
			this.biblioStore.itemSort = newSort;
			this.refreshItemSearch();
		},
		onItemSortReverseUpdate(newSortReverse) {
			this.biblioStore.itemSortReverse = newSortReverse;
			this.refreshItemSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.biblioStore.itemLimit = newLimit;
			this.refreshItemSearch();
		},
		onPageUpdate(newPage) {
			this.biblioStore.itemPage = newPage;
			this.refreshItemSearch();
		},
		refreshItemSearch: debounceFn(() => {
			const biblioStore = useBiblioStore();

			const refreshPromise = biblioStore.refreshItemSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
	},
};

</script>
<style scoped>
.itemsDataTable {
	margin-bottom: 330px;
}
</style>
