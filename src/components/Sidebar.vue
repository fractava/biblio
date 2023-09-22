<template>
	<NcAppNavigation>
		<AppNavigationSelect style="margin-bottom: 22px;">
			<template #icon>
				<LibraryShelves :size="20" />
			</template>
			<template #button-icon>
				<Cog :size="20" />
			</template>
		</AppNavigationSelect>
		<NcAppNavigationItem :title="t('biblio', 'Lend/Return')" to="/lend-return">
			<template #icon>
				<SwapVertical :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :title="t('biblio', 'Mediums')" to="/mediums">
			<template #icon>
				<Bookshelf :size="20" />
			</template>
		</NcAppNavigationItem>
		<NcAppNavigationItem :title="t('biblio', 'Customers')" to="/customers">
			<template #icon>
				<AccountMultiple :size="20" />
			</template>
		</NcAppNavigationItem>
	</NcAppNavigation>
</template>
<script>
import AppNavigationSelect from "./AppNavigationSelect.vue";
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'
import NcAppNavigationSpacer from '@nextcloud/vue/dist/Components/NcAppNavigationSpacer.js'

import SwapVertical from 'vue-material-design-icons/SwapVertical';
import AccountMultiple from 'vue-material-design-icons/AccountMultiple';
import Bookshelf from 'vue-material-design-icons/Bookshelf';
import LibraryShelves from 'vue-material-design-icons/LibraryShelves';
import Cog from 'vue-material-design-icons/Cog';

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
};
</script>
