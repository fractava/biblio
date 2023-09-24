<template>
	<NcAppSettingsSection id="main" title="Main">
		<ul>
			<NcListItem v-for="collection in biblioStore.collections"
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
					<NcActionButton @click="editCollection(collection.id)">
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
</template>

<script>
import { mapStores } from "pinia";
import NcAppSettingsSection from "@nextcloud/vue/dist/Components/NcAppSettingsSection.js";
import NcListItem from "@nextcloud/vue/dist/Components/NcListItem.js";
import NcAppNavigationNewItem from "@nextcloud/vue/dist/Components/NcAppNavigationNewItem.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import Plus from "vue-material-design-icons/Plus";
import LibraryShelves from "vue-material-design-icons/LibraryShelves";
import Pencil from "vue-material-design-icons/Pencil";
import Delete from "vue-material-design-icons/Delete";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
		NcAppSettingsSection,
		NcListItem,
		NcAppNavigationNewItem,
		NcActionButton,
		Plus,
		LibraryShelves,
		Pencil,
		Delete,
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
	},
	methods: {
		async addNewCollection(value) {
			await this.biblioStore.createCollection({ name: value });
			await this.biblioStore.fetchCollections();
		},
		async editCollection(id) {
			this.settingsStore.setSite("collection", {
				collectionId: id,
			});
		},
		async deleteCollection(id) {
			await this.biblioStore.deleteCollection(id);
			await this.biblioStore.fetchCollections();
		},
	},
};
</script>
