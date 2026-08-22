/**
 * Exemplo independente de framework.
 * Adapte persistEvent e enqueueEvent ao banco/fila do projeto.
 */

export interface EvolutionWebhookPayload {
  event?: string;
  instance?: string;
  data?: {
    key?: {
      id?: string;
      fromMe?: boolean;
      remoteJid?: string;
      remoteJidAlt?: string;
    };
    [key: string]: unknown;
  };
  [key: string]: unknown;
}

export interface WebhookDependencies {
  isKnownInstance(instanceName: string): Promise<boolean>;
  persistEvent(input: {
    dedupeKey: string;
    instanceName: string;
    eventName: string;
    messageId?: string;
    payload: EvolutionWebhookPayload;
  }): Promise<{ inserted: boolean }>;
  enqueueEvent(dedupeKey: string): Promise<void>;
}

export async function receiveEvolutionWebhook(
  payload: EvolutionWebhookPayload,
  dependencies: WebhookDependencies,
): Promise<{ status: number; body: Record<string, unknown> }> {
  const eventName = payload.event?.trim().toLowerCase();
  const instanceName = payload.instance?.trim();

  if (!eventName || !instanceName) {
    return {
      status: 400,
      body: { ok: false, error: 'Payload de webhook inválido.' },
    };
  }

  if (!(await dependencies.isKnownInstance(instanceName))) {
    return {
      status: 403,
      body: { ok: false, error: 'Instância não autorizada.' },
    };
  }

  const messageId = payload.data?.key?.id;
  const fallbackId = await sha256(JSON.stringify(payload));
  const dedupeKey = `${instanceName}:${eventName}:${messageId ?? fallbackId}`;

  const persisted = await dependencies.persistEvent({
    dedupeKey,
    instanceName,
    eventName,
    messageId,
    payload,
  });

  if (persisted.inserted) {
    await dependencies.enqueueEvent(dedupeKey);
  }

  return {
    status: 200,
    body: { ok: true, duplicate: !persisted.inserted },
  };
}

async function sha256(value: string): Promise<string> {
  const bytes = new TextEncoder().encode(value);
  const hash = await crypto.subtle.digest('SHA-256', bytes);

  return Array.from(new Uint8Array(hash))
    .map((byte) => byte.toString(16).padStart(2, '0'))
    .join('');
}
