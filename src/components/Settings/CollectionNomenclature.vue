<template>
	<div>
		<NcSelect class="nomenclatureSelect"
			:options="nomenclatureStore.options.item"
			:placeholder="t('biblio', 'Item Nomenclature')"
			:reduce="reduce"
			:value="settingsStore.selectedCollection.nomenclatureItem"
			@input="updateItemNomenclature" />
	</div>
</template>
<script>
import { mapStores } from "pinia";

import NcSelect from "@nextcloud/vue/dist/Components/NcSelect.js";

import { useSettingsStore } from "../../store/settings.js";
import { useNomenclatureStore } from "../../store/nomenclature.js";

export default {
	components: {
		NcSelect,
	},
	computed: {
		...mapStores(useSettingsStore, useNomenclatureStore),
	},
	methods: {
		reduce(option) {
			return option.id;
		},
		updateItemNomenclature(nomenclatureItem) {
			this.settingsStore.updateSelectedCollection({
				nomenclatureItem,
			});
		},
	},
};
</script>
