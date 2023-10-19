<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Address;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\RecordNotFoundException;
use App\Model\Table\AddressesTable;

/**
 * Stores Controller
 */
class StoresController extends AppController
{

    /**
     * add() - Adiciona uma nova loja ao banco de dados.
     *
     * Esta função recebe dados de entrada, cria uma nova entidade de loja e um endereço associado.
     *
     * @return void
     */
    public function add()
    {
        $this->autoRender = false;

        // Cria uma nova entidade Store com os dados da solicitação.
        $store = $this->Stores->newEntity($this->request->getData());

        // Valida a entidade Store.
        $store = $this->Stores->patchEntity($store, $this->request->getData());

        // Verifica erros de validação.
        if ($store->getErrors()) {
            $response = ['success' => false, 'message' => 'Erro de validacao', 'errors' => $store->getErrors()];
            echo json_encode($response);
            return;
        }

        // Inicia uma transação de banco de dados.
        $connection = ConnectionManager::get('default');
        $connection->begin();

        // Salva a entidade Store no banco de dados.
        if ($this->Stores->save($store)) {
            $store_id = $store->id;

            // Prepara dados de endereço.
            $addressData = $this->request->getData('address');
            $addressData['foreign_id'] = $store_id;
            $addressData['foreign_table'] = 'Stores';

            // Cria uma nova entidade Address.
            $address = new Address($addressData);

            // Salva a entidade Address no banco de dados.
            if ($this->Stores->Addresses->save($address)) {
                $connection->commit();
                $response = ['success' => true, 'message' => 'Registro de loja e endereco salvos com sucesso'];
            } else {
                $connection->rollback();
                $response = ['success' => false, 'message' => 'Erro ao salvar o registro de endereco'];
            }
        } else {
            $connection->rollback();
            $response = ['success' => false, 'message' => 'Erro ao salvar o registro de loja'];
        }

        // Retorna uma resposta JSON.
        echo json_encode($response);
    }

    /**
     * index() - Recupera uma lista de todas as lojas e seus endereços associados.
     *
     * @return void
     */
    public function index()
    {
        $this->autoRender = false;

        // Obtém todas as lojas.
        $stores = $this->Stores->find()->all();
        $data = [];

        foreach ($stores as $store) {
            // Obtém os dados da loja.
            $storeData = $store->toArray();

            // Cria uma instância da tabela de Addresses.
            $addressesTable = new AddressesTable();

            // Obtém dados de endereço associados à loja.
            $addressData = $addressesTable->find()
                ->where([
                    'foreign_table' => 'Stores',
                    'foreign_id' => $store->id
                ])
                ->first()
                ->toArray();

            // Adiciona os dados do endereço aos dados da loja.
            $storeData['address'] = $addressData;

            $data[] = $storeData;
        }

        // Retorna uma resposta JSON com os dados das lojas e endereços.
        $response = ['success' => true, 'data' => $data];
        echo json_encode($response);
    }

    /**
     * edit() - Edita o nome de uma loja existente.
     *
     * @return void
     */
    public function edit()
    {
        $this->autoRender = false;

        // Obtém os dados da solicitação.
        $requestData = $this->request->getData();
        $id = $requestData['id'];

        try {
            // Tenta recuperar a loja com base no ID fornecido.
            $store = $this->Stores->get($id);
        } catch (RecordNotFoundException $ex) {
            $response = ['success' => false, 'message' => 'ID de loja nao encontrado'];
            echo json_encode($response);
            return;
        }

        // Obtém o novo nome da loja.
        $newName = $requestData['name'];

        // Verifica se o novo nome não está em uso por outra loja.
        $existingStore = $this->Stores->find()
            ->where(['name' => $newName])
            ->first();

        if ($existingStore && $existingStore->id !== $id) {
            $response = ['success' => false, 'message' => 'Nome da loja ja em uso'];
            echo json_encode($response);
            return;
        }

        if ($store) {
            if ($this->request->is('post')) {
                // Atualiza o nome da loja e salva no banco de dados.
                $store = $this->Stores->patchEntity($store, $requestData);

                if ($this->Stores->save($store)) {
                    $response = ['success' => true, 'message' => 'Nome da loja atualizado com sucesso'];
                } else {
                    $response = ['success' => false, 'message' => 'Erro ao atualizar o nome da loja'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Metodo de solicitacao invalido'];
            }
        } else {
            $response = ['success' => false, 'message' => 'ID de loja invalido'];
        }

        // Retorna uma resposta JSON.
        echo json_encode($response);
    }

    /**
     * delete() - Exclui uma loja e seus registros de endereço associados.
     *
     * @return void
     */
    public function delete()
    {
        $this->autoRender = false;

        // Obtém o ID da loja a ser excluída da solicitação.
        $requestData = $this->request->getData();
        $id = $requestData['id'];

        try {
            // Tenta recuperar a loja com base no ID fornecido.
            $store = $this->Stores->get($id);
        } catch (RecordNotFoundException) {
            $response = ['success' => false, 'message' => 'ID de loja nao encontrado'];
            echo json_encode($response);
            return;
        }

        // Obtém a tabela de Addresses.
        $addressesTable = TableRegistry::getTableLocator()->get('Addresses');

        // Obtém os registros de endereço associados à loja.
        $addresses = $addressesTable->find()
            ->where(['foreign_table' => 'Stores', 'foreign_id' => $id]);

        // Itera sobre os registros de endereço e os exclui.
        foreach ($addresses as $address) {
            $addressesTable->delete($address);
        }

        // Tenta excluir a loja.
        if ($this->Stores->delete($store)) {
            $response = ['success' => true, 'message' => 'Loja e registros de endereco excluidos com sucesso'];
        } else {
            $response = ['success' => false, 'message' => 'Erro ao excluir a loja e registros de endereco'];
        }

        // Retorna uma resposta JSON.
        echo json_encode($response);
    }
}
