<template>
	<div>
		<SectionHeader>{{ t("biblio", "Return") }}</SectionHeader>
		<SimpleTable>
			<tbody>
				<tr>
					<td>
						<NcTextField :label="t('biblio', 'Barcode')"
							:value.sync="currentBarcode"
							@keydown.enter.prevent="returnItemInstance" />
					</td>
				</tr>
			</tbody>
		</SimpleTable>
		<SimpleTableSubmitButon :loading="loading" @click="returnItemInstance" />
	</div>
</template>
<script>
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import SectionHeader from "../components/SectionHeader.vue";
import SimpleTable from "../components/SimpleTable.vue";
import SimpleTableSubmitButon from "../components/SImpleTableSubmitButton.vue";

import { api } from "../api.js";

export default {
	components: {
		NcTextField,
		SectionHeader,
		SimpleTable,
		SimpleTableSubmitButon,
	},
	data() {
		return {
			currentBarcode: "",
			loading: false,
		};
	},
	methods: {
		returnItemInstance() {
			this.loading = true;

			api.deleteLoan(this.$route.params.collectionId, this.currentBarcode)
				.then(() => {
					this.currentBarcode = "";
					this.loading = false;
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not return item instance"));
					this.loading = false;
				});
		},
	},
};
</script>
