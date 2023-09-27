<template>
	<NoCollectionSelected>
		<ul>
			<AddItemModal />
			<Table :columns="['title']"
				:field-columns="includedItemFields"
				:items="biblioStore.items"
				@click="openItem($event)" />
		</ul>
	</NoCollectionSelected>
</template>

<script>
import { mapStores } from "pinia";

import Table from "../components/Table.vue";
import { useBiblioStore } from "../store/biblio.js";
import AddItemModal from "../components/AddItemModal.vue";
import NoCollectionSelected from "../components/NoCollectionSelected.vue";

export default {
	components: {
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
		this.biblioStore.fetchItems();
	},
	methods: {
		openItem(item) {
			this.$router.push({
				path: "/item/" + item,
			});
		},
	},
};

</script>
