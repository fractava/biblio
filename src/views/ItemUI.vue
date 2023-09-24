<template>
	<div>
		<div class="editModeContainer">
			<NcButton aria-label="Start/Stop editing mode"
				@click="editMode = !editMode">
				<template #icon>
					<Pencil :size="20" />
				</template>
				<template>Edit</template>
			</NcButton>
		</div>
		<ShortTextField slot="header"
			:enable-drag-handle="false"
			:field-type="FieldTypes['short']"
			:allow-title-edit="false"
			:is-required="true"
			title="Titel"
			:value="title" />
	</div>
</template>

<script>
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";

import Pencil from "vue-material-design-icons/Pencil.vue";
import FieldTypes from "../models/FieldTypes";
import ShortTextField from "../components/Fields/ShortTextField";

export default {
	components: {
		NcButton,
		Pencil,
		ShortTextField,
	},
	props: {
		title: {
			type: String,
			default: "",
		},
		fields: {
			type: Array,
			default: () => [],
		},
	},
	data() {
		return {
			editMode: false,
			FieldTypes,
		};
	},
};
</script>

<style scoped>

.editModeContainer {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 20px;
}

.ignoreForLayout {
	display: contents;
}
</style>
