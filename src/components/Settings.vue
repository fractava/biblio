<template>
	<NcAppSettingsDialog :open.sync="open" :show-navigation="false" name="Application settings">
		<NcAppSettingsSection id="main" title="Main">
			<NcAppNavigationNewItem title="New Library" @new-item="addNewLibrary">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcAppNavigationNewItem>
		</NcAppSettingsSection>
	</NcAppSettingsDialog>
</template>

<script>
import { mapStores } from "pinia";
import NcAppSettingsDialog from "@nextcloud/vue/dist/Components/NcAppSettingsDialog.js";
import NcAppSettingsSection from "@nextcloud/vue/dist/Components/NcAppSettingsSection.js";
import NcAppNavigationNewItem from "@nextcloud/vue/dist/Components/NcAppNavigationNewItem.js";

import Plus from "vue-material-design-icons/Plus";

import { useMediumsStore } from "../store/mediums.js";

export default {
	components: {
		NcAppSettingsDialog,
		NcAppSettingsSection,
		NcAppNavigationNewItem,
		Plus,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		...mapStores(useMediumsStore),
	},
	methods: {
		addNewLibrary(value) {
			this.mediumsStore.createLibrary({ name: value });
			this.mediumsStore.fetchLibraries();
		},
	},
};
</script>
