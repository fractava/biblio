<template>
	<NoCollectionSelected>
		<ul>
			<AddItemModal :open.sync="modalOpen" />
			<DataTable :columns="columns"
				:rows="biblioStore.itemSearchResults"
				:can-create-rows="true"
				:current-sort.sync="currentSort"
				:current-sort-reverse.sync="currentSortReverse"
				:current-filters="biblioStore.itemFilters"
				:create-row-label="t('biblio', 'Create Item')"
				:create-row-description="t('biblio', 'There are currently no items in this collection, that fit the search parameters')"
				@create-row="modalOpen = true"
				@set-search-string="onItemSearchUpdate"
				@update:currentFilters="onItemFieltersUpdate"
				@click-row="openItem" />
		</ul>
	</NoCollectionSelected>
</template>

<script>
import { mapStores } from "pinia";
import { debounce } from "debounce";
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
			currentSort: "title",
			currentSortReverse: false,
		};
	},
	computed: {
		...mapStores(useBiblioStore),
		includedItemFields() {
			return this.biblioStore.itemFields.filter((itemField) => (itemField.includeInList));
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
		refreshItemSearch() {
			this.biblioStore.refreshItemSearchResults();
		},
	},
};

</script>
