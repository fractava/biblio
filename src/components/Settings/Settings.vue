<template>
	<NcAppSettingsDialog :open="open"
		:show-navigation="showNavigation"
		name="Biblio settings"
		class="biblio-settings-dialog"
		@update:open="openClose">
		<!-- Home -->
		<NcAppSettingsSection v-if="settingsStore.site === 'home'"
			id="collections"
			:name="t('biblio', 'Collections')">
			<CollectionsList />
		</NcAppSettingsSection>

		<!-- Collection -->
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection"
			:name="t('biblio', 'Properties')">
			<CollectionProperties />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-nomenclature"
			:name="t('biblio', 'Nomenclature')">
			<CollectionNomenclature />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-members"
			:name="t('biblio', 'Members')">
			<CollectionMembers />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-loan-until-presets"
			:name="t('biblio', 'Loan Until Presets')">
			<CollectionLoanUntilPresets />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-item-fields"
			:name="t('biblio', 'Item Fields')">
			<CollectionItemFields />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-loan-fields"
			:name="t('biblio', 'Item Instance Loan Fields')">
			<CollectionLoanFields />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-customer-fields"
			:name="t('biblio', 'Customer Fields')">
			<CollectionCustomerFields />
		</NcAppSettingsSection>

		<!-- Import Collection -->
		<NcAppSettingsSection v-if="settingsStore.site === 'import_collection'"
			id="import_collection"
			:name="t('biblio', 'Import collection')">
			<Imports />
		</NcAppSettingsSection>
	</NcAppSettingsDialog>
</template>

<script>
import { mapStores } from "pinia";
import NcAppSettingsDialog from "@nextcloud/vue/dist/Components/NcAppSettingsDialog.js";
import NcAppSettingsSection from "@nextcloud/vue/dist/Components/NcAppSettingsSection.js";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";
import CollectionsList from "./CollectionsList.vue";
import CollectionProperties from "./CollectionProperties.vue";
import CollectionNomenclature from "./CollectionNomenclature.vue";
import CollectionMembers from "./CollectionMembers.vue";
import CollectionLoanUntilPresets from "./CollectionLoanUntilPresets.vue";
import CollectionItemFields from "./CollectionItemFields.vue";
import CollectionLoanFields from "./CollectionLoanFields.vue";
import CollectionCustomerFields from "./CollectionCustomerFields.vue";
import Imports from "./Imports.vue";

export default {
	components: {
		NcAppSettingsDialog,
		NcAppSettingsSection,
		CollectionsList,
		CollectionProperties,
		CollectionNomenclature,
		CollectionMembers,
		CollectionLoanUntilPresets,
		CollectionItemFields,
		CollectionLoanFields,
		CollectionCustomerFields,
		Imports,
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
			return this.settingsStore.site !== "home" && this.settingsStore.site !== "import_collection";
		},
		title() {
			if (this.settingsStore.site === "home") {
				return "Collections";
			} else if (this.settingsStore.site === "collection") {
				return this.settingsStore.selectedCollection?.name + " Collection";
			} else {
				return "";
			}
		},
	},
	methods: {
		async openClose(newState) {
			this.$emit("update:open", newState);

			if (!newState) {
				this.settingsStore.site = "home";
			}
		},
	},
};
</script>

<style>
.biblio-settings-dialog .modal-container {
	margin: 50px;
    min-height: 90vh;
    min-width: 75vw;
}
</style>