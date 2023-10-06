<template>
	<span>{{ value.join(", ") }}</span>
</template>

<script>
import cellMixin from "./cellMixin";

export default {
	mixins: [cellMixin],
};
</script>
