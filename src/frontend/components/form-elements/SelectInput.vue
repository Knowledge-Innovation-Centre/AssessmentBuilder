<template>
  <div>
    <label>
        {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <select class="aoat-w-full"
            :style="getWidthStyle"
            :class="hasError ? 'aoat-border-red-400' : ''"
            v-model="value">
      <option :value="null" disabled hidden>{{ object.placeholder }}</option>
      <option v-for="option in options" :key="option.id" :value="option.id">{{ option.name }}</option>
    </select>
  </div>
</template>

<script>

  export default {

    name: 'SelectInput',


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
      },
      options() {
        return this.object.options
      },
      getWidthStyle() {
        if (this.object.maxWidth) {
          return "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ';'
        }
      }
    },
    methods: {

    }
  };
</script>
