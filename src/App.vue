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
import { useMediumsStore } from "./store/mediums.js";

export default {
	name: "App",
	components: {
		NcAppContent,
		Sidebar,
	},
	computed: {
		...mapStores(useMediumsStore),
	},
	mounted() {
		this.mediumsStore.fetchCollections();
		this.mediumsStore.fetchMediums();
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
