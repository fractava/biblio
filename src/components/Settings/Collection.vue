<template>
	<NcAppSettingsSection id="collection" :title="selectedCollection?.name || 'unknown-collection'">
		{{ selectedCollection }}
	</NcAppSettingsSection>
</template>

<script>
import { mapStores } from "pinia";
import NcAppSettingsSection from "@nextcloud/vue/dist/Components/NcAppSettingsSection.js";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
		NcAppSettingsSection,
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
		selectedCollection() {
			return this.biblioStore.collections.find(collection => collection.id === this.settingsStore.context?.collectionId);
		}
	},
};
</script>
