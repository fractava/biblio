<template>
	<NcAppNavigation>
		<AppNavigationSelect />
		<NcAppNavigationItem :title="t('biblio', 'Lend/Return')" to="/lend-return" icon="icon-medium" />
		<NcAppNavigationItem :title="t('biblio', 'Mediums')" to="/mediums" icon="icon-category-enabled" />
		<NcAppNavigationItem :title="t('biblio', 'Customers')" to="/customers" icon="icon-category-enabled" />
	</NcAppNavigation>
</template>
<script>
import AppNavigationSelect from "./AppNavigationSelect.vue";
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'

export default {
	components: {
		AppNavigationSelect,
		NcAppNavigation,
		NcAppNavigationItem,
	},
};
</script>
