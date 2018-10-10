import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { AdminKnowledgeAreas } from 'Components'
import { adminAreaActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return { knowledgeAreas: state.knowledgeAreas }
}

const mapDispatchToProps = dispatch => {
    const { loadAreas } = sharedActions
    return { actions: bindActionCreators(Object.assign(adminAreaActions, {loadAreas}), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminKnowledgeAreas)
