<template>
	<NoCollectionSelected>
		<ul>
			<NcTextField :value="biblioStore.itemSearch" @update:value="onItemSearchUpdate" />
			<AddItemModal />
			<Table :columns="['title']"
				:field-columns="includedItemFields"
				:items="biblioStore.itemSearchResults"
				@click="openItem($event)" />
		</ul>
	</NoCollectionSelected>
</template>

<script>
import { mapStores } from "pinia";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import Table from "../components/Table.vue";
import { useBiblioStore } from "../store/biblio.js";
import AddItemModal from "../components/AddItemModal.vue";
import NoCollectionSelected from "../components/NoCollectionSelected.vue";

export default {
	components: {
		NcTextField,
		Table,
		AddItemModal,
		NoCollectionSelected,
	},
	computed: {
		...mapStores(useBiblioStore),
		includedItemFields() {
			return this.biblioStore.itemFields.filter((itemField) => (itemField.includeInList));
		},
	},
	mounted() {
		this.biblioStore.refreshItemSearchResults();
	},
	methods: {
		openItem(item) {
			this.$router.push({
				path: "/item/" + item,
			});
		},
		onItemSearchUpdate(newSearch) {
			this.biblioStore.itemSearch = newSearch;
			this.biblioStore.refreshItemSearchResults();
		},
	},
};

</script>
