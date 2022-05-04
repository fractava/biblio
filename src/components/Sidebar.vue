<template>
	<AppNavigation>
		<AppNavigationItem :title="t('biblio', 'Lend/Return')" to="/lend-return" icon="icon-medium" />
		<AppNavigationItem :title="t('biblio', 'Mediums')" to="/mediums" icon="icon-category-enabled" />
		<AppNavigationItem :title="t('biblio', 'Customers')" to="/customers" icon="icon-category-enabled" />
	</AppNavigation>
</template>
<script>

import AppNavigation from "@nextcloud/vue/dist/Components/AppNavigation";
import AppNavigationItem from "@nextcloud/vue/dist/Components/AppNavigationItem";

export default {
	components: {
		AppNavigation,
		AppNavigationItem,
	},
};
</script>
