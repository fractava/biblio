<template>
	<NcAppSettingsDialog :open="open"
		:show-navigation="showNavigation"
		name="Application settings"
		@update:open="openClose">
		<NcAppSettingsSection id="error" title="Unknown site" />
		<CollectionsList v-if="settingsStore.site === 'home'" />
		<Collection v-else-if="settingsStore.site === 'collection'" />
	</NcAppSettingsDialog>
</template>

<script>
import { mapStores } from "pinia";
import NcAppSettingsDialog from "@nextcloud/vue/dist/Components/NcAppSettingsDialog.js";
import NcAppSettingsSection from "@nextcloud/vue/dist/Components/NcAppSettingsSection.js";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";
import CollectionsList from "./CollectionsList.vue";
import Collection from "./Collection.vue";

export default {
	components: {
		NcAppSettingsDialog,
		NcAppSettingsSection,
		CollectionsList,
		Collection,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
		showNavigation() {
			return this.settingsStore.site !== "home";
		},
	},
	methods: {
		async openClose(newState) {
			this.$emit("update:open", newState);
		},
	},
};
</script>
