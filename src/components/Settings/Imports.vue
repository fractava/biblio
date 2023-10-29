<template>
	<div>
		<ul>
			<NcListItem v-if="!settingsStore.context.importMethod"
				:title="t('biblio', 'Import from v1')"
				:bold="false"
				:active="false"
				@click="selectImportMethod('v1')">
				<template #icon>
					<Numeric1CircleOutline :size="20" />
				</template>
				<template #subtitle>
					{{ t('biblio', 'Import a phpmyadmin JSON export of a database used with v1 of this app (before the nextcloud rewrite)') }}
				</template>
			</NcListItem>
		</ul>
		<V1Import v-if="settingsStore.context.importMethod === 'v1'" />
	</div>
</template>
<script>
import Vue from "vue";
import { mapStores } from "pinia";
import NcListItem from "@nextcloud/vue/dist/Components/NcListItem.js";

import Numeric1CircleOutline from "vue-material-design-icons/Numeric1CircleOutline.vue";

import { useSettingsStore } from "../../store/settings.js";

import V1Import from "./V1Import.vue";

export default {
	components: {
		NcListItem,
		Numeric1CircleOutline,
		V1Import,
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	methods: {
		selectImportMethod(methodId) {
			Vue.set(this.settingsStore.context, "importMethod", methodId);
		},
	},
};
</script>
