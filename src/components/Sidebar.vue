<template>
	<NcAppNavigation>
		<AppNavigationSelect style="margin-bottom: 12px;"
			:options="mediumsStore.collections"
			options-label="name"
			button-aria-label="Open Collection Settings"
			placeholder="Select Collection"
			@button-clicked="settingsOpen = !settingsOpen"
			@input="(selection) => { mediumsStore.selectCollection(selection.id) }">
			<template #button-icon>
				<Cog :size="20" />
			</template>
		</AppNavigationSelect>
		<Settings :open="settingsOpen" />
		<NcAppNavigationItem :name="t('biblio', 'Lend/Return')" to="/lend-return">
			<template #icon>
				<SwapVertical :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :name="t('biblio', 'Mediums')" to="/mediums">
			<template #icon>
				<Bookshelf :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :name="t('biblio', 'Customers')" to="/customers">
			<template #icon>
				<AccountMultiple :size="20" />
			</template>
		</NcAppNavigationItem>
	</NcAppNavigation>
</template>
<script>
import AppNavigationSelect from "./AppNavigationSelect.vue";
import Settings from "./Settings.vue";

import { mapStores } from "pinia";
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation";
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem";

import SwapVertical from "vue-material-design-icons/SwapVertical";
import AccountMultiple from "vue-material-design-icons/AccountMultiple";
import Bookshelf from "vue-material-design-icons/Bookshelf";
import Cog from "vue-material-design-icons/Cog";

import { useMediumsStore } from "../store/mediums.js";

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
		...mapStores(useMediumsStore),
	},
};
</script>
