<template>
	<p>
		{{ settingsStore.selectedCollection }}
	</p>
</template>

<script>
import { mapStores } from "pinia";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
	},
};
</script>
