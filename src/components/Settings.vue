<template>
	<NcAppSettingsDialog :open="open" @update:open="openClose" :show-navigation="false" name="Application settings">
		<NcAppSettingsSection id="main" title="Main">
			<ul>
				<NcListItem v-for="collection in itemsStore.collections"
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
						<NcActionButton @click="deleteCollection(collection.id)">
							<template #icon>
								<Delete :size="20" />
							</template>
							Delete
						</NcActionButton>
					</template>
				</NcListItem>
			</ul>
			<NcAppNavigationNewItem title="New Collection" @new-item="addNewCollection">
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

import { useItemsStore } from "../store/biblio.js";

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
		...mapStores(useItemsStore),
	},
	methods: {
		async addNewCollection(value) {
			await this.itemsStore.createCollection({ name: value });
			await this.itemsStore.fetchCollections();
		},
		async deleteCollection(id) {
			await this.itemsStore.deleteCollection(id);
			await this.itemsStore.fetchCollections();
		},
		async openClose(newState) {
			this.$emit("update:open", newState);
		}
	},
};
</script>
