<template>
  <div>
    <label>
        {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <select class="aoat-w-full"
            :class="hasError ? 'aoat-border-red-400' : ''"
            :style="getWidthStyle"
            v-model="value">
      <option :value="null" disabled hidden>{{ object.placeholder }}</option>
      <option v-for="option in options" :key="option.id" :value="option.id">{{ getOptionName(option) }}</option>
    </select>
    </div>
</template>

<script>

  export default {

    name: 'CountryInput',


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
      getOptionName(option) {
        if (!this.object.labelParts) {
          return option.name
        }
        let labels = [];
        for (let labelPart of this.object.labelParts) {
          labels.push(option[labelPart.key])
        }
        console.log(option);
        console.log(this.object.labelParts);
        return labels.join(' ' + this.object.labelPartsSeperator + ' ')
      }
    }
  };
</script>
