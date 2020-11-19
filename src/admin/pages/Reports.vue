<template>
  <div class="app-reports">
    <h2>Reports</h2>
    Please select form:

    <select v-model="selectedForm">
      <option v-for="form in forms" :key="form.ID" :value="form">
        {{form.post_title}}
      </option>
    </select>

    <table>
      <thead>
      <tr>
        <th>User</th>
        <th :key="scoreLabel" v-for="scoreLabel in scoreLabels">{{ scoreLabel }}</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="assessment in assessments" :key="assessment.ID">
        <td>
          {{ assessment.ID }}
        </td>
        <th :key="scoreValue" v-for="scoreValue in scoreValues">{{ scoreValue }}</th>
      </tr>

      </tbody>
    </table>
  </div>
</template>

<script>
import Api from "../Api";

export default {

  name: 'Reports',

  components: {
  },

  data () {
    return {
      forms: [],
      selectedForm: null,
      assessments: [],
      scoreLabels: [],
      scoreValues: [],
      alreadyIncludedElementsForScores: [],
    };
  },

  watch: {
    async selectedForm() {
      await Api.get(aoat_config.aoatGetFormUrl + this.selectedForm.ID + '/assessments').then((result) => {
        this.assessments = result.data
        this.scoreLabels= [];
        this.scoreValues= [];

        console.log(this.selectedForm.form_data);
        console.log(this.assessments[0]);
        this.calculateScore(this.selectedForm.form_data[0].items, this.assessments[0])
      })
    }
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      await Api.get(aoat_config.aoatGetFormUrl).then((result) => {
        this.forms = result.data
      })
    },

    calculateScore(items, assessment) {
      for (let item of items) {

        // if(!this.checkConditions(item)) {
        //   continue;
        // }
        if (item.items) {
          this.calculateScore(item.items, assessment)
          continue;
        }

        let value = assessment[item.reportItemKey]


        if (item.disableForScoring) {
          continue;
        }

        if (!item.options && !item.optionsVertical) {
          continue;
        }
        if (this.alreadyIncludedElementsForScores.includes(item.reportItemKey)) {
          continue
        }
        this.alreadyIncludedElementsForScores.push(item.reportItemKey)


        let maxScore = 0;


          this.scoreLabels.push(this.getItemLabel(item))


        if (item.type === 'radio_grid') {
          for (let option of item.optionsVertical) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score)
            }
          }
          this.totalScore += maxScore * item.optionsHorizontal.length

          if (!value) {
            continue;
          }
          let localScore = 0;
          for (let option of item.optionsHorizontal) {
            let verticalOption = item.optionsVertical.find(optionVertical => optionVertical.id === value[option.id])
            if (verticalOption) {
              this.score += parseInt(verticalOption.score)
              localScore += parseInt(verticalOption.score)
            }
          }

          this.scoreValues.push(localScore)

        } else if(item.options) {
          for (let option of item.options) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score)
            }
          }

          this.totalScore += maxScore

          if (!value) {
            continue;
          }
          let verticalOption = item.options.find(optionVertical => optionVertical.id === value)

          let localScore = 0;
          if (verticalOption) {
            this.score += parseInt(verticalOption.score)
            localScore += parseInt(verticalOption.score)
          }

          this.scoreValues.push(localScore)
        }
      }

      return true
    },

    getItemLabel(item) {
      if (item.reportLabel &&  item.reportLabel !== '') {
        return item.reportLabel
      }
      return item.label
    },

  }
};
</script>

<style lang="css" scoped>
th {
  max-width: 300px;
}
</style>
