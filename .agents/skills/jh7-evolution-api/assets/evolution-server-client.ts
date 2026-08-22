/**
 * Cliente servidor para Evolution API v2.3.x.
 * NÃO importe este arquivo em componentes React executados no navegador.
 */

export interface EvolutionClientConfig {
  baseUrl: string;
  apiKey: string;
  timeoutMs?: number;
}

export interface EvolutionApiErrorData {
  status: number;
  endpoint: string;
  message: string;
  details?: unknown;
}

export class EvolutionApiError extends Error {
  readonly status: number;
  readonly endpoint: string;
  readonly details?: unknown;

  constructor(data: EvolutionApiErrorData) {
    super(data.message);
    this.name = 'EvolutionApiError';
    this.status = data.status;
    this.endpoint = data.endpoint;
    this.details = data.details;
  }
}

export interface SendTextInput {
  instanceName: string;
  number: string;
  text: string;
}

export interface CreateInstanceInput {
  instanceName: string;
  integration?: 'WHATSAPP-BAILEYS' | string;
  qrcode?: boolean;
}

function normalizeBaseUrl(value: string): string {
  const trimmed = value.trim().replace(/\/+$/, '');

  if (!/^https?:\/\//i.test(trimmed)) {
    throw new Error('EVOLUTION_API_URL deve iniciar com http:// ou https://');
  }

  return trimmed;
}

function normalizeInstanceName(value: string): string {
  const normalized = value.trim();

  if (!normalized) {
    throw new Error('O nome da instância é obrigatório.');
  }

  return normalized;
}

export function normalizeWhatsAppNumber(value: string): string {
  const number = value.replace(/\D/g, '');

  if (number.length < 10 || number.length > 15) {
    throw new Error('Número do WhatsApp inválido. Informe país, DDD e número.');
  }

  return number;
}

export class EvolutionServerClient {
  private readonly baseUrl: string;
  private readonly apiKey: string;
  private readonly timeoutMs: number;

  constructor(config: EvolutionClientConfig) {
    this.baseUrl = normalizeBaseUrl(config.baseUrl);
    this.apiKey = config.apiKey.trim();
    this.timeoutMs = config.timeoutMs ?? 15_000;

    if (!this.apiKey) {
      throw new Error('EVOLUTION_API_KEY não foi configurada.');
    }
  }

  async fetchInstances<T = unknown>(): Promise<T> {
    return this.request<T>('/instance/fetchInstances', { method: 'GET' });
  }

  async connectionState<T = unknown>(instanceName: string): Promise<T> {
    const instance = encodeURIComponent(normalizeInstanceName(instanceName));
    return this.request<T>(`/instance/connectionState/${instance}`, {
      method: 'GET',
    });
  }

  async connect<T = unknown>(instanceName: string): Promise<T> {
    const instance = encodeURIComponent(normalizeInstanceName(instanceName));
    return this.request<T>(`/instance/connect/${instance}`, { method: 'GET' });
  }

  async createInstance<T = unknown>(input: CreateInstanceInput): Promise<T> {
    return this.request<T>('/instance/create', {
      method: 'POST',
      body: JSON.stringify({
        instanceName: normalizeInstanceName(input.instanceName),
        integration: input.integration ?? 'WHATSAPP-BAILEYS',
        qrcode: input.qrcode ?? true,
      }),
    });
  }

  async sendText<T = unknown>(input: SendTextInput): Promise<T> {
    const instance = encodeURIComponent(normalizeInstanceName(input.instanceName));
    const text = input.text.trim();

    if (!text) {
      throw new Error('O texto da mensagem é obrigatório.');
    }

    return this.request<T>(`/message/sendText/${instance}`, {
      method: 'POST',
      body: JSON.stringify({
        number: normalizeWhatsAppNumber(input.number),
        text,
      }),
    });
  }

  private async request<T>(endpoint: string, init: RequestInit): Promise<T> {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), this.timeoutMs);

    try {
      const response = await fetch(`${this.baseUrl}${endpoint}`, {
        ...init,
        signal: controller.signal,
        headers: {
          'Content-Type': 'application/json',
          apikey: this.apiKey,
          ...init.headers,
        },
      });

      const contentType = response.headers.get('content-type') ?? '';
      const payload: unknown = contentType.includes('application/json')
        ? await response.json().catch(() => null)
        : await response.text().catch(() => '');

      if (!response.ok) {
        throw new EvolutionApiError({
          status: response.status,
          endpoint,
          message: this.toSafeMessage(response.status),
          details: payload,
        });
      }

      return payload as T;
    } catch (error) {
      if (error instanceof EvolutionApiError) {
        throw error;
      }

      if (error instanceof DOMException && error.name === 'AbortError') {
        throw new EvolutionApiError({
          status: 504,
          endpoint,
          message: 'A Evolution API não respondeu dentro do tempo esperado.',
        });
      }

      throw new EvolutionApiError({
        status: 502,
        endpoint,
        message: 'Não foi possível comunicar com a Evolution API.',
        details: error instanceof Error ? error.message : undefined,
      });
    } finally {
      clearTimeout(timeout);
    }
  }

  private toSafeMessage(status: number): string {
    switch (status) {
      case 400:
      case 422:
        return 'A Evolution API rejeitou os dados enviados.';
      case 401:
      case 403:
        return 'A Evolution API recusou a autenticação.';
      case 404:
        return 'Instância, recurso ou rota não encontrado na Evolution API.';
      case 409:
        return 'A operação entrou em conflito com o estado atual da instância.';
      case 429:
        return 'A Evolution API recebeu solicitações em excesso.';
      default:
        return status >= 500
          ? 'A Evolution API apresentou uma falha interna.'
          : 'A solicitação para a Evolution API falhou.';
    }
  }
}
