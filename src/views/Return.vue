<template>
	<div>
		<SectionHeader>{{ t("biblio", "Return") }}</SectionHeader>
		<NcTextField label="Barcode"
			:value.sync="currentBarcode"
			:show-trailing-button="true"
			trailing-button-icon="arrowRight"
			@trailing-button-click="returnItemInstance"
			@keydown.enter.prevent="returnItemInstance" />
	</div>
</template>
<script>
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import SectionHeader from "../components/SectionHeader.vue";

import { api } from "../api.js";

export default {
	components: {
		SectionHeader,
		NcTextField,
	},
	data() {
		return {
			currentBarcode: "",
		};
	},
	methods: {
		returnItemInstance() {
			api.deleteLoan(this.$route.params.collectionId, this.currentBarcode)
				.then(() => {
					this.currentBarcode = "";
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not return item instance"));
				});
		},
	},
};
</script>
