<template>
	<div>
		<p>{{ $route.params.id }}</p>
		<p>{{ createNew }}</p>
	</div>
</template>

<script>

export default {
	props: {
		createNew: {
			type: Boolean,
			default: false,
		},
	},
}
</script>
