<template>
	<NoCollectionSelected>
		<ul>
			<AddItemModal />
			<Table :columns="['title', 'fields']"
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
