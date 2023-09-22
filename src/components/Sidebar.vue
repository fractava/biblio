<template>
	<NcAppNavigation>
		<AppNavigationSelect style="margin-bottom: 22px;"
			:options="mediumsStore.libraries"
			options-label="name"
			button-aria-label="Open Library Settings"
			placeholder="Select Library">
			<template #button-icon>
				<Cog :size="20" />
			</template>
		</AppNavigationSelect>
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
import { mapStores } from "pinia";
import AppNavigationSelect from "./AppNavigationSelect.vue";
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'
import NcAppNavigationSpacer from '@nextcloud/vue/dist/Components/NcAppNavigationSpacer.js'

import SwapVertical from 'vue-material-design-icons/SwapVertical';
import AccountMultiple from 'vue-material-design-icons/AccountMultiple';
import Bookshelf from 'vue-material-design-icons/Bookshelf';
import LibraryShelves from 'vue-material-design-icons/LibraryShelves';
import Cog from 'vue-material-design-icons/Cog';

import { useMediumsStore } from "../store/mediums.js";

export default {
	components: {
		AppNavigationSelect,
		NcAppNavigation,
		NcAppNavigationItem,
		NcAppNavigationSpacer,
		SwapVertical,
		AccountMultiple,
		Bookshelf,
		LibraryShelves,
		Cog,
	},
	computed: {
		...mapStores(useMediumsStore),

	}
};
</script>
