<template>
	<div>
		<NcEmptyContent v-if="biblioStore.collections.length === 0"
			:title="t('biblio', 'No collections')">
			<template #icon>
				<GridOff />
			</template>
			<template #description>
				{{ t('biblio', 'You currently don’t have access to any collection. Create or import one below.') }}
			</template>
		</NcEmptyContent>
		<ul>
			<NcListItem v-for="collection in biblioStore.collections"
				:key="collection.id"
				:title="collection.name"
				:bold="false"
				:active="false"
				@click="editCollection(collection.id)">
				<template #icon>
					<LibraryShelves :size="20" />
				</template>
				<template #actions>
					<NcActionButton @click="editCollection(collection.id)">
						<template #icon>
							<Pencil :size="20" />
						</template>
						{{ t('biblio', 'Edit') }}
					</NcActionButton>
					<NcActionButton @click="deleteCollection(collection.id)">
						<template #icon>
							<Delete :size="20" />
						</template>
						{{ t('biblio', 'Delete') }}
					</NcActionButton>
				</template>
			</NcListItem>
			<NcListItem :title="t('biblio', 'Create new collection')"
				:bold="false"
				:active="false"
				@click="modalOpen = true">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcListItem>
			<AddCollectionModal :open.sync="modalOpen" />
			<NcListItem :title="t('biblio', 'Import collection')"
				:bold="false"
				:active="false"
				@click="importCollection">
				<template #icon>
					<Import :size="20" />
				</template>
			</NcListItem>
		</ul>
	</div>
</template>

<script>
import { mapStores } from "pinia";
import NcListItem from "@nextcloud/vue/dist/Components/NcListItem.js";
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem.js";
import NcAppNavigationNewItem from "@nextcloud/vue/dist/Components/NcAppNavigationNewItem.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import NcEmptyContent from "@nextcloud/vue/dist/Components/NcEmptyContent.js";

import Plus from "vue-material-design-icons/Plus.vue";
import LibraryShelves from "vue-material-design-icons/LibraryShelves.vue";
import Pencil from "vue-material-design-icons/Pencil.vue";
import Delete from "vue-material-design-icons/Delete.vue";
import Import from "vue-material-design-icons/Import.vue";
import GridOff from "vue-material-design-icons/GridOff.vue";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

import AddCollectionModal from "./AddCollectionModal.vue";

export default {
	components: {
		NcListItem,
		NcAppNavigationItem,
		NcAppNavigationNewItem,
		NcActionButton,
		NcEmptyContent,
		Plus,
		LibraryShelves,
		Pencil,
		Delete,
		Import,
		GridOff,
		AddCollectionModal,
	},
	data() {
		return {
			modalOpen: false,
		};
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
	},
	methods: {
		importCollection() {
			this.settingsStore.setSite("import_collection", {});
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
