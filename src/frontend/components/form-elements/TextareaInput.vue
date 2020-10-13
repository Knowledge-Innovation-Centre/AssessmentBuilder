<template>
  <div>
    <label>
        {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <textarea class="aoat-w-full"
              :class="hasError ? 'aoat-border-red-400' : ''"
              :placeholder="object.placeholder"
              v-model="value" />
  </div>
</template>

<script>
  export default {

    name: 'TextareaInput',

    components: {

    },
    props: {
      object: {
        type: Object,
        required: true,
      },
      hasError: {
        type: Boolean,
        required: false,
        default: false,
      }
    },

    data () {
      return {
      };
    },

    computed: {
      value: {
        get () {
          return this.$store.state.assessment[this.object.key]
        },
        set (newValue) {
          return this.$store.dispatch('updateValue', { key: this.object.key, value: newValue})
        }
      }
    },
    methods: {
    }
  };
</script>
