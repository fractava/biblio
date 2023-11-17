<template>
	<NcActions class="addFieldButton"
		primary
		:open.sync="open"
		:menu-name="t('biblio', 'Add a field')"
		default-icon="icon-add">
		<NcActionButton v-for="(field, type) in FieldTypes"
			:key="field.label"
			:close-after-click="true"
			:icon="field.icon"
			@click="addField(type, field)">
			{{ field.label }}
			<template #icon>
				<Icon :is="field.iconComponent" :size="20" />
			</template>
		</NcActionButton>
	</NcActions>
</template>

<script>
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import FieldTypes from "../../models/FieldTypes.js";

export default {
	components: {
		NcActions,
		NcActionButton,
	},
	data() {
		return {
			FieldTypes,
			open: false,
		};
	},
	methods: {
		addField(type, field) {
			this.$emit("add-field", type, field);
		},
	},
};
</script>

<style scoped>
.addFieldButton {
	width: calc(100% - 24px);
	margin-left: 24px;
	margin-top: 5px;
	transition: opacity 0.5s;
}

.addFieldButton >>> button {
	width: 100% !important;
}
</style>
