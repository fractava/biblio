<template>
	<div id="content" class="app-biblio">
		<Sidebar />
		<NcAppContent>
			<div id="biblio-main-content">
				<router-view />
			</div>
		</NcAppContent>
	</div>
</template>

<script>
import NcAppContent from "@nextcloud/vue/dist/Components/NcAppContent";
import "@nextcloud/dialogs/dist/index.css";
import { mapStores } from "pinia";

import Sidebar from "./components/Sidebar.vue";
import { useBiblioStore } from "./store/biblio.js";
import { useItemsStore } from "./store/items.js";
import { useItemInstancesStore } from "./store/itemInstances.js";
import { useCustomersStore } from "./store/customers.js";

export default {
	name: "App",
	components: {
		NcAppContent,
		Sidebar,
	},
	computed: {
		...mapStores(useBiblioStore),
	},
	mounted() {
		this.biblioStore.fetchCollections();

		const route = window.biblioRouter.currentRoute;

		if (route.params.collectionId) {
			this.biblioStore.selectedCollectionId = parseInt(route.params.collectionId);
		}

		window.biblioRouter.afterEach((to, from) => {
			if (from.params.collectionId !== to.params.collectionId) {
				const biblioStore = useBiblioStore();

				biblioStore.selectedCollectionId = parseInt(to.params.collectionId);
				this.refreshStores();
			}
		});

		this.refreshStores();
	},
	methods: {
		refreshStores() {
			const itemsStore = useItemsStore();
			const itemInstancesStore = useItemInstancesStore();
			const customersStore = useCustomersStore();

			itemsStore.fetchFields();
			itemsStore.refreshSearchResults().catch(() => {});
			itemInstancesStore.fetchFields();
			itemInstancesStore.refreshSearchResults().catch(() => {});
			customersStore.fetchFields();
			customersStore.refreshSearchResults().catch(() => {});
		},
	},
};
</script>
<style scoped>
	#biblio-main-content {
		padding-left: 4% !important;
		padding-right: 4% !important;
		padding-top: 7px !important;
	}
</style>
