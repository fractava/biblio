<template>
	<div>
		<NcTextField :value.sync="collectionName"
			:error="!collectionNameValid"
			:label-visible="true"
			:helper-text="collectionNameValid ? '' : 'Ungültiger Name'"
			label="Name"
			:show-trailing-button="collectionNameHasChanges"
			trailing-button-icon="arrowRight"
			style=" --color-border-maxcontrast: #949494;"
			@trailing-button-click="onNameSave"
			@blur="() => collectionName = collectionName.trim()"
			@keyup.enter="onNameSave" />
	</div>
</template>

<script>
import { mapStores } from "pinia";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
		NcTextField,
	},
	data() {
		return {
			collectionName: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
		collectionNameValid() {
			return this.collectionName.trim().length > 3;
		},
		collectionNameHasChanges() {
			return this.collectionName.trim() !== this.settingsStore.selectedCollection.name;
		},
	},
	mounted() {
		this.collectionName = this.settingsStore?.selectedCollection?.name;
	},
	methods: {
		onNameSave() {
			this.biblioStore.updateCollection(this.settingsStore.context?.collectionId, {
				name: this.collectionName,
			});
		},
	},
};
</script>
