<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Client;
use Cake\Http\Client\Exception\NetworkException;

/**
 * Addresses Controller
 *
 */
class AddressesController extends AppController
{

    public function add()
    {
        // Define que a renderização automática da view está desativada.
        $this->autoRender = false;

        // Obtém os dados da solicitação POST.
        $requestData = $this->request->getData();

        // Valida o formato do CEP.
        if (!preg_match('/^\d{8}$/', $requestData['postal_code'])) {
            $response = ['success' => false, 'message' => 'CEP invalido'];
            echo json_encode($response);
            return;
        }

        // Consulta a API CEP LA.
        $cepLaClient = new Client();
        try {
            $cepLaResponse = $cepLaClient->get("https://cepla.com.br/ws/{$requestData['postal_code']}/json");
        } catch (NetworkException $e) {
            $cepLaResponse = null;
        }

        if ($cepLaResponse && $cepLaResponse->isOk()) {
            // Obtém os dados do endereço da resposta da API.
            $cepLaAddressData = $cepLaResponse->getJson();

            if (!empty($cepLaAddressData)) {
                // Atualiza os dados do endereço com os dados obtidos.
                $requestData['state'] = $cepLaAddressData['uf'];
                $requestData['city'] = $cepLaAddressData['localidade'];
                $requestData['sublocality'] = $cepLaAddressData['bairro'];
                $requestData['street'] = $cepLaAddressData['logradouro'];
            }
        } else {
            $cep = $requestData['postal_code'];

            // Consulta a API ViaCEP.
            $viaCepClient = new Client();
            $viaCepResponse = $viaCepClient->get("https://viacep.com.br/ws/{$cep}/json");

            if ($viaCepResponse->isOk()) {
                // Obtém os dados do endereço da resposta da API.
                $addressData = $viaCepResponse->getJson();

                if (isset($addressData['erro']) && $addressData['erro'] === true) {
                    $response = ['success' => false, 'message' => 'CEP nao encontrado'];
                } else {
                    // Atualiza os dados do endereço com os dados obtidos.
                    $requestData['state'] = $addressData['uf'];
                    $requestData['city'] = $addressData['localidade'];
                    $requestData['sublocality'] = $addressData['bairro'];
                    $requestData['street'] = $addressData['logradouro'];

                    // Cria uma nova entidade de endereço.
                    $address = $this->Addresses->newEntity($requestData);

                    // Verifica erros de validação.
                    $errors = $address->getErrors();
                }
            } else {
                $response = ['success' => false, 'message' => 'Erro ao consultar CEP'];
            }
        }

        if (empty($errors)) {
            // Verifica se já existe um endereço com o mesmo 'foreign_id'.
            $existingAddress = $this->Addresses->find()
                ->where(['foreign_id' => $requestData['foreign_id']])
                ->first();

            if ($existingAddress) {
                // Atualiza os campos do endereço existente.
                $existingAddress->set($requestData);

                if ($this->Addresses->save($existingAddress)) {
                    $response = ['success' => true, 'message' => 'Campos preenchidos com sucesso'];
                } else {
                    $response = ['success' => false, 'message' => 'Erro ao salvar os campos'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Registro nao encontrado com o foreign_id especificado'];
            }
        } else {
            // Se houver erros de validação, retorna a lista de erros.
            $response = ['success' => false, 'errors' => $errors];
        }

        // Retorna a resposta em formato JSON.
        echo json_encode($response);
    }

    public function view()
    {
        // Define que a renderização automática da view está desativada.
        $this->autoRender = false;

        // Obtém todos os endereços e converte para um array.
        $addresses = $this->Addresses->find('all')->toArray();

        // Formata o CEP com máscara.
        foreach ($addresses as &$address) {
            $address['postal_code_masked'] = substr($address['postal_code'], 0, 5) . '-' . substr($address['postal_code'], 5);
        }

        // Retorna a lista de endereços em formato JSON.
        echo json_encode(['success' => true, 'data' => $addresses]);
    }
}
