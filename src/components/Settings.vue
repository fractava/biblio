<template>
	<NcAppSettingsDialog :open.sync="open" :show-navigation="false" name="Application settings">
		<NcAppSettingsSection id="main" title="Main">
			<ul>
				<NcListItem v-for="collection in mediumsStore.libraries"
					:key="collection.id"
					:title="collection.name"
					:bold="false"
					:active="false">
					<template #icon>
						<LibraryShelves :size="20" />
					</template>
					<template #subname>
						In this slot you can put both text and other components such as icons
					</template>
					<template #actions>
						<NcActionButton @click="alert('Edit')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Edit
						</NcActionButton>
						<NcActionButton @click="deleteLibrary(collection.id)">
							<template #icon>
								<Delete :size="20" />
							</template>
							Delete
						</NcActionButton>
					</template>
				</NcListItem>
			</ul>
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
import NcListItem from "@nextcloud/vue/dist/Components/NcListItem.js";
import NcAppNavigationNewItem from "@nextcloud/vue/dist/Components/NcAppNavigationNewItem.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import Plus from "vue-material-design-icons/Plus";
import LibraryShelves from "vue-material-design-icons/LibraryShelves";
import Pencil from "vue-material-design-icons/Pencil";
import Delete from "vue-material-design-icons/Delete";

import { useMediumsStore } from "../store/mediums.js";

export default {
	components: {
		NcAppSettingsDialog,
		NcAppSettingsSection,
		NcListItem,
		NcAppNavigationNewItem,
		NcActionButton,
		Plus,
		LibraryShelves,
		Pencil,
		Delete,
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
		async addNewLibrary(value) {
			await this.mediumsStore.createLibrary({ name: value });
			await this.mediumsStore.fetchLibraries();
		},
		async deleteLibrary(id) {
			await this.mediumsStore.deleteLibrary(id);
			await this.mediumsStore.fetchLibraries();
		},
	},
};
</script>
