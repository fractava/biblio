<template>
	<NcAppNavigation>
		<NcAppNavigationItem :title="t('biblio', 'Lend/Return')" to="/lend-return" icon="icon-medium" />
		<NcAppNavigationItem :title="t('biblio', 'Mediums')" to="/mediums" icon="icon-category-enabled" />
		<NcAppNavigationItem :title="t('biblio', 'Customers')" to="/customers" icon="icon-category-enabled" />
	</NcAppNavigation>
</template>
<script>

import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'

export default {
	components: {
		NcAppNavigation,
		NcAppNavigationItem,
	},
};
</script>
