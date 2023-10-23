<template>
	<NcAppSettingsDialog :open="open"
		:show-navigation="showNavigation"
		title="Biblio settings"
		class="biblio-settings-dialog"
		@update:open="openClose">
		<!-- Home -->
		<NcAppSettingsSection v-if="settingsStore.site === 'home'"
			id="collections"
			title="Collections">
			<CollectionsList />
			<V1Import />
		</NcAppSettingsSection>

		<!-- Collection -->
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection"
			title="Properties">
			<CollectionProperties />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-members"
			title="Members">
			<CollectionMembers />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-item-fields"
			title="Item Fields">
			<CollectionItemFields />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-loan-fields"
			title="Item Instance Loan Fields">
			<CollectionLoanFields />
		</NcAppSettingsSection>
		<NcAppSettingsSection v-if="settingsStore.site === 'collection'"
			id="collection-customer-fields"
			title="Customer Fields">
			<CollectionCustomerFields />
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
import V1Import from "./V1Import.vue";
import CollectionProperties from "./CollectionProperties.vue";
import CollectionMembers from "./CollectionMembers.vue";
import CollectionItemFields from "./CollectionItemFields.vue";
import CollectionLoanFields from "./CollectionLoanFields.vue";
import CollectionCustomerFields from "./CollectionCustomerFields.vue";

export default {
	components: {
		NcAppSettingsDialog,
		NcAppSettingsSection,
		CollectionsList,
		V1Import,
		CollectionProperties,
		CollectionMembers,
		CollectionItemFields,
		CollectionLoanFields,
		CollectionCustomerFields,
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