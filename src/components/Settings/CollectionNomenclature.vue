<template>
	<div class="nomenclatureSettings">
		<NomenclatureSelect :options="nomenclatureStore.options.item"
			:placeholder="t('biblio', 'Item Nomenclature')"
			:value="settingsStore.selectedCollection.nomenclatureItem"
			@input="updateItemNomenclature" />
		<NomenclatureSelect :options="nomenclatureStore.options.instance"
			:placeholder="t('biblio', 'Instance Nomenclature')"
			:value="settingsStore.selectedCollection.nomenclatureInstance"
			@input="updateInstanceNomenclature" />
		<NomenclatureSelect :options="nomenclatureStore.options.customer"
			:placeholder="t('biblio', 'Customer Nomenclature')"
			:value="settingsStore.selectedCollection.nomenclatureCustomer"
			@input="updateCustomerNomenclature" />
	</div>
</template>
<script>
import { mapStores } from "pinia";

import { useSettingsStore } from "../../store/settings.js";
import { useNomenclatureStore } from "../../store/nomenclature.js";

import NomenclatureSelect from "./NomenclatureSelect.vue";

export default {
	components: {
		NomenclatureSelect,
	},
	computed: {
		...mapStores(useSettingsStore, useNomenclatureStore),
	},
	methods: {
		updateItemNomenclature(nomenclatureItem) {
			this.settingsStore.updateSelectedCollection({
				nomenclatureItem,
			});
		},
		updateInstanceNomenclature(nomenclatureInstance) {
			this.settingsStore.updateSelectedCollection({
				nomenclatureInstance,
			});
		},
		updateCustomerNomenclature(nomenclatureCustomer) {
			this.settingsStore.updateSelectedCollection({
				nomenclatureCustomer,
			});
		},
	},
};
</script>
<style scoped>
.nomenclatureSettings {
	display: flex;
	flex-direction: column;
}
</style>
