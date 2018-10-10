import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { LecturerProfile } from 'Components'
import { lecturerActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        profile: state.lecturerProfile,
        degrees: state.degrees,
        departments: state.departments,
        allAreas: state.knowledgeAreas,
        uid: state.auth.user.uid
    }
}

const mapDispatchToProps = dispatch => {
    const { loadAreas, loadDegrees, loadDepartments } = sharedActions
    return { actions: bindActionCreators({...lecturerActions, loadAreas, loadDegrees, loadDepartments}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(LecturerProfile)
