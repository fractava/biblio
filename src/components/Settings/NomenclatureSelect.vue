<template>
	<vueSelect :options="options"
		:placeholder="placeholder"
		:reduce="reduce"
		:value="value"
		:clearable="false"
		@input="(newSelection) => $emit('input', newSelection)">
		<template #option="option">
			<NomenclatureOption :option="option" />
		</template>
		<template #selected-option="option">
			<NomenclatureOption :option="option" />
		</template>
	</vueSelect>
</template>
<script>
import vueSelect from "vue-select";

import NomenclatureOption from "./NomenclatureOption.vue";

export default {
	components: {
		vueSelect,
		NomenclatureOption,
	},
	props: {
		options: {
			type: Array,
			default: () => ([]),
		},
		placeholder: {
			type: String,
			default: "",
		},
		value: {
			type: String,
			default: "",
		},
	},
	methods: {
		reduce(option) {
			return option.id;
		},
	},
};
</script>
