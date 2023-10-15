<template>
	<input ref="fileInput"
		type="file"
		@change="onFilePicked">
</template>

<script>
import { api } from "../../api.js";

export default {
	data() {
		return {
			data: "",
		};
	},
	methods: {
		onPickFile() {
			this.$refs.fileInput.click();
		},
		onFilePicked(event) {
			const files = event.target.files;
			const fileReader = new FileReader();
			fileReader.addEventListener("load", () => {
				const parsed = JSON.parse(fileReader.result);
				console.log(parsed);
				api.importV1(parsed);
			});
			fileReader.readAsText(files[0]);
		},
	},
};
</script>
