const visitorKey = 'toolkitly.visitor_id';

function visitorId() {
    let id = localStorage.getItem(visitorKey);

    if (!id) {
        id = crypto.randomUUID();
        localStorage.setItem(visitorKey, id);
    }

    return id;
}

function analyticsUrl() {
    return document.querySelector('meta[name="toolkitly-analytics-url"]')?.content || '/api/analytics/tool-events';
}

export function recordToolEvent(tool, action = 'success', metadata = {}) {
    if (!tool) {
        return;
    }

    const payload = {
        tool,
        action,
        status: 'success',
        metadata,
        visitor_id: visitorId(),
    };

    window.gtag?.('event', action, {
        event_category: 'tool_success',
        event_label: tool,
        tool_name: tool,
        ...metadata,
    });

    fetch(analyticsUrl(), {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
        keepalive: true,
    }).catch(() => {});
}
