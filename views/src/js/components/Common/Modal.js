// I should have made this component a longggg time ago.

import React, { Component } from 'react'

class Modal extends Component {
    render() {
        const { modalId, size, title, onSubmit, onCancel, children, submitText, cancelText } = this.props
        return <div id={modalId} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby={`${modalId}-title`}>
            <div class={`modal-dialog ${size || 'modal-lg'}`} role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick={onCancel}>
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id={`${modalId}-title`}>{title}</h4>
                    </div>
                    <form class="form-horizontal" onSubmit={onSubmit}>
                        <div class="modal-body">
                            {children}
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{submitText || 'Đồng ý'}</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" onClick={onCancel}>{cancelText || 'Bỏ qua'}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    }
}

export default Modal
