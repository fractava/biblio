<template>
	<NcAppNavigation>
		<AppNavigationSelect style="margin-bottom: 12px;"
			:options="biblioStore.collections"
			:value="selectValue"
			options-label="name"
			button-aria-label="Open Collection Settings"
			placeholder="Select Collection"
			@button-clicked="settingsOpen = !settingsOpen"
			@input="collectionSelected">
			<template #button-icon>
				<Cog :size="20" />
			</template>
		</AppNavigationSelect>
		<Settings :open.sync="settingsOpen" />
		<NcAppNavigationItem :name="t('biblio', 'Lend/Return')" :to="linkIfCollectionIdSelected('/lend-return')">
			<template #icon>
				<SwapVertical :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :name="nomenclatureStore.items" :to="linkIfCollectionIdSelected('/items')">
			<template #icon>
				<component :is="nomenclatureStore.itemIcon" :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :name="nomenclatureStore.instances" :to="linkIfCollectionIdSelected('/iteminstances')">
			<template #icon>
				<component :is="nomenclatureStore.instanceIcon" :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :name="t('biblio', 'Customers')" :to="linkIfCollectionIdSelected('/customers')">
			<template #icon>
				<component :is="nomenclatureStore.customerIcon" :size="20" />
			</template>
		</NcAppNavigationItem>
	</NcAppNavigation>
</template>
<script>
import AppNavigationSelect from "./AppNavigationSelect.vue";
import Settings from "./Settings/Settings.vue";

import { mapStores } from "pinia";
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation";
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem";

import SwapVertical from "vue-material-design-icons/SwapVertical";
import AccountMultiple from "vue-material-design-icons/AccountMultiple";
import Bookshelf from "vue-material-design-icons/Bookshelf";
import Cog from "vue-material-design-icons/Cog";

import { useBiblioStore } from "../store/biblio.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

export default {
	components: {
		AppNavigationSelect,
		Settings,
		NcAppNavigation,
		NcAppNavigationItem,
		SwapVertical,
		AccountMultiple,
		Bookshelf,
		Cog,
	},
	data() {
		return {
			settingsOpen: false,
		};
	},
	computed: {
		...mapStores(useBiblioStore, useNomenclatureStore),
		selectValue() {
			if (this.$route.params.collectionId) {
				return parseInt(this.$route.params.collectionId);
			} else {
				return null;
			}
		},
	},
	methods: {
		linkIfCollectionIdSelected(subpath) {
			if (this.$route.params.collectionId) {
				return "/" + this.$route.params.collectionId + subpath;
			} else {
				return undefined;
			}
		},
		collectionSelected(selectedId) {
			if (selectedId) {
				if (this.$route.params.collectionId && Object.keys(this.$route.params).length === 1) {
					this.$router.push({
						params: {
							collectionId: selectedId,
						},
					});
				} else {
					this.$router.push({
						name: "lend-return",
						params: {
							collectionId: selectedId,
						},
					});
				}
			} else {
				this.$router.push({
					name: "home",
				});
			}
		},
	},
};
</script>
