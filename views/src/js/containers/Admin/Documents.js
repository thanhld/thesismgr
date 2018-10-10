import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminDocuments } from 'Components'
import { adminDocumentActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return { documents: state.adminDocuments }
}

const mapDispatchToProps = dispatch => {
    const { uploadFile } = sharedActions
    return {
        actions: bindActionCreators(Object.assign(adminDocumentActions, {uploadFile}), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminDocuments)
