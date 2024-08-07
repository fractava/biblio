<template>
	<NcAppNavigation>
		<AppNavigationSelect :options="biblioStore.collections"
			:value="selectValue"
			options-label="name"
			:button-aria-label="t('biblio', 'Open Collection Settings')"
			:placeholder="t('biblio', 'Select Collection')"
			@button-clicked="settingsOpen = !settingsOpen"
			@input="collectionSelected">
			<template #button-icon>
				<Cog :size="20" />
			</template>
		</AppNavigationSelect>
		<Settings :open.sync="settingsOpen" />
		<NcAppNavigationList>
			<NcAppNavigationItem :name="t('biblio', 'Lend/Return')" :to="linkIfCollectionIdSelected('/loan-return')">
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
			<NcAppNavigationItem :name="nomenclatureStore.customers" :to="linkIfCollectionIdSelected('/customers')">
				<template #icon>
					<component :is="nomenclatureStore.customerIcon" :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :name="t('biblio', 'History')" :to="linkIfCollectionIdSelected('/history')">
				<template #icon>
					<History :size="20" />
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>
	</NcAppNavigation>
</template>
<script>
import AppNavigationSelect from "./AppNavigationSelect.vue";
import Settings from "./Settings/Settings.vue";

import { mapStores } from "pinia";
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation.js";
import NcAppNavigationList from '@nextcloud/vue/dist/Components/NcAppNavigationList.js'
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem.js";

import SwapVertical from "vue-material-design-icons/SwapVertical.vue";
import AccountMultiple from "vue-material-design-icons/AccountMultiple.vue";
import Bookshelf from "vue-material-design-icons/Bookshelf.vue";
import Cog from "vue-material-design-icons/Cog.vue";
import History from "vue-material-design-icons/History.vue";

import { useBiblioStore } from "../store/biblio.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

export default {
	components: {
		AppNavigationSelect,
		Settings,
		NcAppNavigation,
		NcAppNavigationList,
		NcAppNavigationItem,
		SwapVertical,
		AccountMultiple,
		Bookshelf,
		Cog,
		History,
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
						name: "loan-return",
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
