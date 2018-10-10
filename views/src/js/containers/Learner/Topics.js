import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { LearnerTopics } from 'Components'
import { sharedActions, learnerActions } from 'Actions'

const mapStateToProps = state => {
    return {
        uid: state.auth.user.uid,
        lecturers: state.lecturers,
        outOfficers: state.outOfficers,
        departments: state.departments,
        topics: state.learnerTopics,
        degrees: state.degrees,
        quotas: state.quotas
    }
}

const mapDispatchToProps = dispatch => {
    const { createOutOfficers, cancelChangeRequest, uploadAttachment, loadTopics } = learnerActions
    const { loadLecturers, loadDepartments, loadDegrees, createActivity, loadOutOfficers, loadActiveQuota } = sharedActions
    return {
        actions: bindActionCreators({loadTopics, loadLecturers, loadDepartments, loadDegrees, loadOutOfficers, createActivity, createOutOfficers, cancelChangeRequest, loadActiveQuota, uploadAttachment}, dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(LearnerTopics)
