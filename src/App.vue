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

import Sidebar from "./components/Sidebar.vue";

export default {
	name: "App",
	components: {
		NcAppContent,
		Sidebar,
	},
	computed: {
	},
	mounted() {
		this.$store.dispatch("fecthMediums");
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
