<template>
	<NcEmptyContent v-if="!$route.params.collectionId"
		:name="t('biblio', 'No collection selected')">
		<template #icon>
			<Magnify />
		</template>
		<template #description>
			{{ t('biblio', 'Select a collection in the sidebar') }}
		</template>
	</NcEmptyContent>
	<div v-else>
		<slot />
	</div>
</template>

<script>
import { mapStores } from "pinia";
import NcEmptyContent from "@nextcloud/vue/dist/Components/NcEmptyContent.js"

import Magnify from "vue-material-design-icons/Magnify";

import { useBiblioStore } from "../store/biblio.js";

export default {
	components: {
		NcEmptyContent,
		Magnify,
	},
	computed: {
		...mapStores(useBiblioStore),
	},
};
</script>