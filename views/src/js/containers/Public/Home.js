import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { Home } from 'Components'
import { sharedActions, adminAnnoucementActions } from 'Actions'

const mapStateToProps = state => {
    return {
        announcements: state.announcements,
        user: state.auth.user
    }
}

const mapDispatchToProps = dispatch => {
    const { loadAnnouncements } = sharedActions
    const { adminLoadAnnouncements, createAnnouncement, updateAnnouncement, deleteAnnouncement } = adminAnnoucementActions
    return { actions: bindActionCreators({loadAnnouncements, adminLoadAnnouncements, createAnnouncement, updateAnnouncement, deleteAnnouncement}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(Home)
